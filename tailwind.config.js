module.exports = {
    theme: {
        extend: {},
        customForms: theme => ({
            default: {
                checkbox: {
                    width: theme("spacing.6"),
                    height: theme("spacing.6"),
                    backgroundColor: theme("colors.gray.700"),
                    borderColor: theme("colors.transparent"),
                },
            },
        }),
    },
    variants: {},
    plugins: [require("@tailwindcss/custom-forms")],
};
