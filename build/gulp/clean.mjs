import del from "del";

export default function clean() {
    return () => {
        return del(["./uploads/assets/*", "!./uploads/assets/{.gitignore,.gitkeep}"]);
    };
}
