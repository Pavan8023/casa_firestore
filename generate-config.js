import fs from "fs";
import path from "path";

console.log("Starting config generation...");

const dir = "./public";
if (!fs.existsSync(dir)) {
  fs.mkdirSync(dir, { recursive: true });
  console.log("Created /public directory");
}

const configContent = `window.env = {
  FIREBASE_API_KEY: "${process.env.FIREBASE_API_KEY}",
  FIREBASE_AUTH_DOMAIN: "${process.env.FIREBASE_AUTH_DOMAIN}",
  FIREBASE_PROJECT_ID: "${process.env.FIREBASE_PROJECT_ID}",
  FIREBASE_STORAGE_BUCKET: "${process.env.FIREBASE_STORAGE_BUCKET}",
  FIREBASE_MESSAGING_SENDER_ID: "${process.env.FIREBASE_MESSAGING_SENDER_ID}",
  FIREBASE_APP_ID: "${process.env.FIREBASE_APP_ID}",
  FIREBASE_MEASUREMENT_ID: "${process.env.FIREBASE_MEASUREMENT_ID}"
};`;

fs.writeFileSync(path.join(dir, "config.js"), configContent);
console.log("config.js generated successfully with API_KEY:", process.env.FIREBASE_API_KEY ? "SET" : "MISSING");
