module.exports = () => {
  "use strict";

  const del = require("del");

  return async () => {
    return del(["./uploads/assets/*", "!./uploads/assets/{.gitignore,.gitkeep}"]);
  };
};
