import figlet from "figlet";
import chalk from "chalk";

// Create decorative border
const createBorder = (width, char = '═') => char.repeat(width);
const createSideBorder = (text, width) => {
  const padding = Math.max(0, width - text.length - 4);
  const leftPad = Math.floor(padding / 2);
  const rightPad = padding - leftPad;
  return `║ ${' '.repeat(leftPad)}${text}${' '.repeat(rightPad)} ║`;
};

// Generate ASCII art
const asciiArt = figlet.textSync("BNS", {
  horizontalLayout: "default",
  font: "Big" // You can try: "Big", "Standard", "Slant", "3D-ASCII", "Doom"
});

// Calculate dimensions for the box
const artLines = asciiArt.split('\n');
const maxWidth = Math.max(...artLines.map(line => line.length));
const boxWidth = Math.max(maxWidth + 4, 50);

// Create beautiful display
console.log('\n');
console.log(chalk.cyan('╔' + createBorder(boxWidth, '═') + '╗'));
console.log(chalk.cyan(createSideBorder('', boxWidth)));

// Display ASCII art with gradient effect
artLines.forEach((line, index) => {
  const colors = ['magenta', 'blue', 'cyan', 'green', 'yellow'];
  const colorIndex = index % colors.length;
  const paddedLine = line.padEnd(maxWidth);
  console.log(chalk.cyan('║ ') + chalk[colors[colorIndex]].bold(paddedLine) + chalk.cyan(' ║'));
});

console.log(chalk.cyan(createSideBorder('', boxWidth)));
console.log(chalk.cyan('╠' + createBorder(boxWidth, '═') + '╣'));

// Animated loading message with sparkles
const messages = [
  "✨ Initializing Nesa Process ✨",
  "🚀 Loading modules and dependencies",
  "⚡ Optimizing performance settings",
  "🔧 Configuring system parameters"
];

console.log(chalk.cyan(createSideBorder('', boxWidth)));
messages.forEach((message, index) => {
  setTimeout(() => {
    console.log(chalk.cyan(createSideBorder(chalk.green.bold(message), boxWidth)));
  }, index * 800);
});

setTimeout(() => {
  console.log(chalk.cyan(createSideBorder('', boxWidth)));
  console.log(chalk.cyan('╚' + createBorder(boxWidth, '═') + '╝'));

  // Final status message with custom rainbow effect
  const finalMessage = "🎯 Process Nesa Started Successfully! 🎯";
  const rainbowColors = ['red', 'yellow', 'green', 'cyan', 'blue', 'magenta'];

  let coloredMessage = '';
  const messageText = finalMessage.replace(/🎯/g, ''); // Remove emojis for coloring

  for (let i = 0; i < messageText.length; i++) {
    const colorIndex = i % rainbowColors.length;
    const char = messageText[i];
    if (char === ' ') {
      coloredMessage += char;
    } else {
      coloredMessage += chalk[rainbowColors[colorIndex]](char);
    }
  }

  console.log('\n🎯 ' + coloredMessage + ' 🎯');
  console.log(chalk.gray('─'.repeat(finalMessage.length - 4))); // Subtract 4 for emoji characters
  console.log('\n');
}, messages.length * 800 + 500);

// Optional: Add some visual flair with animated dots
let dotCount = 0;
const loadingInterval = setInterval(() => {
  const dots = '.'.repeat((dotCount % 4));
  process.stdout.write(`\r${chalk.yellow('Processing')}${chalk.cyan(dots)}${' '.repeat(3 - dots.length)}`);
  dotCount++;
}, 300);

// Stop the animation after the messages are done
setTimeout(() => {
  clearInterval(loadingInterval);
  process.stdout.write('\r' + ' '.repeat(20) + '\r'); // Clear the loading line
}, messages.length * 800 + 1000);
