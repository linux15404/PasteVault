/* Мини-подсветка синтаксиса без внешних библиотек.
   Поддерживает: php, python, javascript, json, bash, sql. */

(function () {
    const ESC = (s) => s.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");

    // Правила: [язык, список правил (regex + css-класс)]
    const RULES = {
        php: [
            [/(\/\/[^\n]*|#[^\n]*)/g, "c-comment"],
            [/\/\*[\s\S]*?\*\//g, "c-comment"],
            [/'(?:\\.|[^'\\])*'|"(?:\\.|[^"\\])*"/g, "c-string"],
            [/\b(function|class|return|if|else|elseif|foreach|as|while|new|public|private|protected|static|final|readonly|const|use|namespace|echo|print|require|include|declare|true|false|null|match|fn|try|catch|throw|interface|extends|implements|abstract|instanceof|isset|empty|array|int|string|bool|float|void|self|parent)\b/g, "c-keyword"],
            [/\$[A-Za-z_]\w*/g, "c-var"],
            [/\b\d[\d_]*(?:\.\d+)?\b/g, "c-num"],
            [/<\?php|\?>/g, "c-tag"],
        ],
        python: [
            [/(#[^\n]*)/g, "c-comment"],
            [/"""[\s\S]*?"""|'''[\s\S]*?'''/g, "c-string"],
            [/'(?:\\.|[^'\\])*'|"(?:\\.|[^"\\])*"/g, "c-string"],
            [/\b(def|class|return|if|elif|else|for|while|import|from|as|with|try|except|finally|raise|pass|lambda|yield|None|True|False|and|or|not|in|is|async|await|global|nonlocal|assert|del|self)\b/g, "c-keyword"],
            [/\b\d[\d_]*(?:\.\d+)?\b/g, "c-num"],
            [/@\w+/g, "c-tag"],
        ],
        javascript: [
            [/(\/\/[^\n]*)/g, "c-comment"],
            [/\/\*[\s\S]*?\*\//g, "c-comment"],
            [/`(?:\\.|[^`\\])*`|'(?:\\.|[^'\\])*'|"(?:\\.|[^"\\])*"/g, "c-string"],
            [/\b(const|let|var|function|return|if|else|for|while|class|extends|new|this|import|export|from|default|async|await|try|catch|finally|throw|typeof|instanceof|null|undefined|true|false|switch|case|break|continue|do|of|in)\b/g, "c-keyword"],
            [/\b\d+(?:\.\d+)?\b/g, "c-num"],
        ],
        json: [
            [/\"(?:\\.|[^"\\])*\"(?=\s*:)/g, "c-var"],
            [/"(?:\\.|[^"\\])*"/g, "c-string"],
            [/\b(true|false|null)\b/g, "c-keyword"],
            [/-?\b\d+(?:\.\d+)?\b/g, "c-num"],
        ],
        bash: [
            [/(#[^\n]*)/g, "c-comment"],
            [/'(?:[^'\\])*'|"(?:\\.|[^"\\])*"/g, "c-string"],
            [/^\s*[\w-]+(?==)/gm, "c-var"],
            [/^\s*\$\s|^\s*\$\(/gm, "c-tag"],
            [/\$\{?\w+\}?/g, "c-var"],
            [/\b(if|then|else|fi|for|do|done|while|case|esac|function|echo|export|local|return|source)\b/g, "c-keyword"],
        ],
        sql: [
            [/(--[^\n]*)/g, "c-comment"],
            [/'(?:''|[^'])*'/g, "c-string"],
            [/\b(SELECT|FROM|WHERE|INSERT|INTO|VALUES|UPDATE|SET|DELETE|CREATE|TABLE|INDEX|DROP|ALTER|ADD|PRIMARY|KEY|NOT|NULL|DEFAULT|AND|OR|IN|LIKE|ORDER|BY|GROUP|LIMIT|JOIN|LEFT|RIGHT|INNER|OUTER|ON|AS|IF|EXISTS|UNIQUE|TEXT|INTEGER|REAL|BLOB|AUTOINCREMENT)\b/gi, "c-keyword"],
            [/\b\d+\b/g, "c-num"],
        ],
    };

    const STYLE = document.createElement("style");
    STYLE.textContent = `
        .c-comment { color: #6a7d8f; font-style: italic; }
        .c-string  { color: #9ece8c; }
        .c-keyword { color: #7aa2f7; font-weight: 600; }
        .c-var     { color: #e0af78; }
        .c-num     { color: #ff9e64; }
        .c-tag     { color: #bb9af7; }
    `;
    document.head.appendChild(STYLE);

    function highlight(code, lang) {
        const rules = RULES[lang];
        if (!rules) return null;

        // Собираем один общий regex с именованными группами
        const names = rules.map((_, i) => "r" + i);
        const combined = new RegExp(rules.map((r, i) => `(${r[0].source})`).join("|"), rules[0][0].flags.includes("g") ? "g" : "");

        let out = "";
        let last = 0;
        let m;
        while ((m = combined.exec(code)) !== null) {
            out += ESC(code.slice(last, m.index));
            for (let i = 1; i < m.length; i++) {
                if (m[i] !== undefined) {
                    out += `<span class="${rules[i - 1][1]}">${ESC(m[i])}</span>`;
                    break;
                }
            }
            last = m.index + m[0].length;
            if (m[0].length === 0) combined.lastIndex++; // защита от зацикливания
        }
        out += ESC(code.slice(last));
        return out;
    }

    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll("pre.code code[data-lang]").forEach((el) => {
            const lang = el.dataset.lang;
            const html = highlight(el.textContent, lang);
            if (html !== null) el.innerHTML = html;
        });
    });
})();
