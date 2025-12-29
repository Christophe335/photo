const fs = require("fs");
const path = "c:/laragon/www/photo/js/devis.js";
const s = fs.readFileSync(path, "utf8");
const m = s.match(/`/g) || [];
console.log("backticks:", m.length);
// show last 80 chars
console.log("tail:", s.slice(-200));
// show position of last backtick
console.log("lastIndex:", s.lastIndexOf("`"));
