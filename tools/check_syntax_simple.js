const fs = require("fs");
const s = fs.readFileSync("c:/laragon/www/photo/js/devis.js", "utf8");
let stack = []; // store {char,line,col}
let line = 1;
let col = 0;
for (let i = 0; i < s.length; i++) {
  const ch = s[i];
  if (ch === "\n") {
    line++;
    col = 0;
    continue;
  }
  col++;
  const top = stack[stack.length - 1];
  if (ch === "`") {
    if (top && top.char === "`") stack.pop();
    else stack.push({ char: "`", line, col });
  } else if (ch === "(") {
    stack.push("(");
  } else if (ch === ")") {
    if (top === "(") stack.pop();
    else {
      console.log("MISMATCH ) at", line, col);
      break;
    }
  } else if (ch === "{") {
    stack.push({ char: "{", line, col });
  } else if (ch === "}") {
    if (top && top.char === "{") stack.pop();
    else {
      console.log("MISMATCH } at", line, col);
      break;
    }
  } else if (ch === "[") {
    stack.push({ char: "[", line, col });
  } else if (ch === "]") {
    if (top && top.char === "[") stack.pop();
    else {
      console.log("MISMATCH ] at", line, col);
      break;
    }
  }
}
console.log("stack at end length", stack.length);
if (stack.length > 0) console.log("top item", stack[stack.length - 1]);
// print last 200 chars
console.log("tail snippet:\n", s.slice(-400));
