tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            colors: {
                primary: "#0d9488",
                primary_hover: "#0f766e",
                "background-light": "#f3f4f6",
                "background-dark": "#111827",
                "surface-light": "#ffffff",
                "surface-dark": "#1f2937",
            },
            fontFamily: {
                display: ["Inter", "sans-serif"],
                body: ["Inter", "sans-serif"],
            },
            borderRadius: { DEFAULT: "0.5rem" },
        },
    },
};