import { deleteAsync } from "del";

export default function clean() {
    return () => {
        return deleteAsync(["./uploads/assets/*", "!./uploads/assets/{.gitignore,.gitkeep}"]);
    };
}
