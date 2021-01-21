var $ = require('jquery');

global.$ = global.jQuery = $;

import Filter from "./modules/Filter.js";

new Filter(document.querySelector(".js-filter"));

require('bootstrap');



