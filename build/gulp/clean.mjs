import { deleteAsync } from "del";

export default () => {
    return () => {
        return deleteAsync(["./uploads/assets/*", "!./uploads/assets/{.gitignore,.gitkeep}"]);
    };
};
