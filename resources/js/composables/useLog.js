const icons = {
    SUCCESS: "✅",
    INFO: "ℹ️",
    WARNING: "🤔",
    ERROR: "⛔️",
};
const states = {
    SUCCESS: { icons: icons.SUCCESS, color: "#4CAF50" },
    INFO: { icons: icons.INFO, color: "#2196F3" },
    ERROR: { icons: icons.ERROR, color: "#F44336" },
    WARNING: { icons: icons.WARNING, color: "#FF9800" },
};
/**
 * A function that logs a message to the console with a specific style.
 * @param content - The content you want to log.
 * @param [type=info] - The type of log, which can be info, warning, error, success
 */
function useLog(content, type = "info") {
    const state = states[type.toUpperCase()];
    const icon = state.icons;
    const style = `background: ${state.color};
            color:#FFF;
            padding:5px;
            width:100%;
            border-radius: 5px;
            line-height: 26px;
           `;
    console.log(`%c${icon} ${content}`, `${style}`);
}

export default useLog;
