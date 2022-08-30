const del = require("del");

module.exports = () => {
  "use strict";

  return async () => {
    return del(["./uploads/assets/*", "!./uploads/assets/{.gitignore,.gitkeep}"]);
  };
};
