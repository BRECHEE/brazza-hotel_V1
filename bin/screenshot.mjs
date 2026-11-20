/**
 * Development-only screenshot helper using the Edge DevTools protocol.
 * Usage:
 *   node bin/screenshot.mjs <url> <output.png> [width] [height] [full]
 */
import { spawn } from "node:child_process";
import { writeFile } from "node:fs/promises";
import { mkdtemp } from "node:fs/promises";
import { tmpdir } from "node:os";
import { join } from "node:path";

const [url, output, widthArg = "1440", heightArg = "1100", fullArg = "0"] = process.argv.slice(2);
if (!url || !output) {
    console.error("Usage: node bin/screenshot.mjs <url> <output.png> [width] [height] [full]");
    process.exit(1);
}

const width = Number(widthArg);
const height = Number(heightArg);
const fullPage = fullArg === "1";
const port = 9333;
const profile = await mkdtemp(join(tmpdir(), "brazza-cdp-"));
const edge = "C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe";

const browser = spawn(edge, [
    "--headless=new",
    "--disable-gpu",
    "--hide-scrollbars",
    `--remote-debugging-port=${port}`,
    `--user-data-dir=${profile}`,
    "--no-first-run",
    "about:blank",
], { stdio: "ignore", windowsHide: true });

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

async function waitForTarget() {
    for (let attempt = 0; attempt < 60; attempt++) {
        try {
            const response = await fetch(`http://127.0.0.1:${port}/json/list`);
            const targets = await response.json();
            const page = targets.find((target) => target.type === "page");
            if (page?.webSocketDebuggerUrl) return page;
        } catch {
            // Browser still booting.
        }
        await sleep(250);
    }
    throw new Error("Edge DevTools target did not become available.");
}

const target = await waitForTarget();
const socket = new WebSocket(target.webSocketDebuggerUrl);
await new Promise((resolve, reject) => {
    socket.addEventListener("open", resolve, { once: true });
    socket.addEventListener("error", reject, { once: true });
});

let counter = 0;
const pending = new Map();
const eventWaiters = new Map();

socket.addEventListener("message", (event) => {
    const message = JSON.parse(event.data);
    if (message.id && pending.has(message.id)) {
        const { resolve, reject } = pending.get(message.id);
        pending.delete(message.id);
        if (message.error) reject(new Error(message.error.message));
        else resolve(message.result);
        return;
    }
    const waiters = eventWaiters.get(message.method);
    if (waiters) {
        eventWaiters.delete(message.method);
        waiters.forEach((resolve) => resolve(message.params));
    }
});

const send = (method, params = {}) => new Promise((resolve, reject) => {
    const id = ++counter;
    pending.set(id, { resolve, reject });
    socket.send(JSON.stringify({ id, method, params }));
});

const waitForEvent = (method, timeout = 20000) => new Promise((resolve) => {
    const waiters = eventWaiters.get(method) ?? [];
    waiters.push(resolve);
    eventWaiters.set(method, waiters);
    setTimeout(resolve, timeout);
});

await send("Page.enable");
await send("Runtime.enable");

if (process.env.SCREENSHOT_THEME) {
    await send("Page.addScriptToEvaluateOnNewDocument", {
        source: `try { localStorage.setItem("brazza-theme", ${JSON.stringify(process.env.SCREENSHOT_THEME)}); } catch (error) {}`,
    });
}

if (process.env.SCREENSHOT_COOKIE) {
    await send("Network.enable");
    const separator = process.env.SCREENSHOT_COOKIE.indexOf("=");
    await send("Network.setCookie", {
        name: process.env.SCREENSHOT_COOKIE.slice(0, separator),
        value: process.env.SCREENSHOT_COOKIE.slice(separator + 1),
        domain: "localhost",
        path: "/",
        httpOnly: true,
        sameSite: "Lax",
    });
}

await send("Emulation.setDeviceMetricsOverride", {
    width,
    height,
    deviceScaleFactor: 1,
    mobile: width < 768,
});
await send("Emulation.setEmulatedMedia", {
    features: [{ name: "prefers-reduced-motion", value: "no-preference" }],
});

const loaded = waitForEvent("Page.loadEventFired");
await send("Page.navigate", { url });
await loaded;
await send("Runtime.evaluate", { expression: "document.fonts.ready", awaitPromise: true });
await sleep(1200);

if (process.env.SCREENSHOT_TOGGLE === "1") {
    const result = await send("Runtime.evaluate", {
        expression: `(() => {
            const button = document.querySelector("[data-theme-toggle]");
            if (button) button.click();
            return {
                theme: document.documentElement.dataset.theme || "dark",
                pressed: button?.getAttribute("aria-pressed") || null,
                label: button?.getAttribute("aria-label") || null,
                stored: localStorage.getItem("brazza-theme"),
            };
        })()`,
        returnByValue: true,
    });
    console.log("TOGGLE", JSON.stringify(result.result?.value));
    await sleep(500);
}

const layout = await send("Runtime.evaluate", {
    expression: `({
        width: window.innerWidth,
        scrollWidth: document.documentElement.scrollWidth,
        theme: document.documentElement.dataset.theme || "dark",
    })`,
    returnByValue: true,
});
console.log("LAYOUT", JSON.stringify(layout.result?.value));

if (fullPage) {
    await send("Runtime.evaluate", {
        expression: `(async () => {
            const step = Math.max(500, window.innerHeight * 0.7);
            for (let y = 0; y < document.documentElement.scrollHeight; y += step) {
                window.scrollTo(0, y);
                await new Promise((resolve) => setTimeout(resolve, 90));
            }
            window.scrollTo(0, document.documentElement.scrollHeight);
            await new Promise((resolve) => setTimeout(resolve, 500));
            window.scrollTo(0, 0);
            await new Promise((resolve) => setTimeout(resolve, 350));

            document.querySelectorAll("img[loading='lazy']").forEach((image) => {
                image.loading = "eager";
            });
            await Promise.all(Array.from(document.images).map((image) => {
                if (image.complete) return Promise.resolve();
                return new Promise((resolve) => {
                    image.addEventListener("load", resolve, { once: true });
                    image.addEventListener("error", resolve, { once: true });
                });
            }));
            await new Promise((resolve) => setTimeout(resolve, 500));
            window.scrollTo(0, 0);
        })()`,
        awaitPromise: true,
    });
    await sleep(1000);
}

const metrics = await send("Page.getLayoutMetrics");
const content = metrics.cssContentSize ?? metrics.contentSize;
const captureHeight = fullPage ? Math.ceil(content.height) : height;
const result = await send("Page.captureScreenshot", {
    format: "png",
    captureBeyondViewport: fullPage,
    clip: { x: 0, y: 0, width, height: captureHeight, scale: 1 },
});
await writeFile(output, Buffer.from(result.data, "base64"));
console.log(`${output} ${width}x${captureHeight}`);
await send("Browser.close").catch(() => {});
socket.close();
browser.kill();
