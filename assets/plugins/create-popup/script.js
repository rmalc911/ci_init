/**
 * @param {HTMLElement} popup Popup container
 * @param {String} trigger Open popup trigger selector
 * @param {Number} autoOpen Auto Open delay
 * @returns {{
 * 	popup: HTMLElement,
 * 	openPopup: Function,
 * 	dismissPopup: Function,
 * 	on: {
 * 		open: Function,
 * 		close: Function
 * 	}
 * }}
 */
function setUpPopup(popup, trigger = "", autoOpen = false) {
   if (!popup) return;
   if (autoOpen)
      setTimeout(() => {
         openPopup();
      }, parseInt(autoOpen));

   let on = {
      open: function (obj = null) {},
      close: function (obj = null) {}
   };

   popup.querySelectorAll(".popup-dismiss").forEach((dismiss) => {
      dismiss.addEventListener("click", dismissPopup);
   });

   if (trigger) {
      document.querySelectorAll(trigger).forEach((triggerElement) => {
         triggerElement.addEventListener("click", openPopup);
      });
   }

   /**
    * @param {MouseEvent} e
    */
   function dismissPopup(e = null) {
      popup.classList.add("popup-closing");
      setTimeout(() => {
         popup.classList.remove("popup-active", "popup-closing");
      }, 500);
      document.documentElement.classList.remove("popup-open");
      on.close();
      window.activePopup = null;
   }

   /**
    * @param {MouseEvent} e
    */
   function openPopup(e = null) {
      if (e) e.preventDefault();
      let scrollbarWidth = window.innerWidth - document.body.clientWidth + "px";
      document.documentElement.setAttribute(
         "style",
         "--scrollbar-width: " + scrollbarWidth
      );
      if (window.activePopup && window.activePopup.dismissPopup) {
         window.activePopup.dismissPopup();
      }
      popup.classList.add("popup-active");
      document.documentElement.classList.add("popup-open");
      on.open(e);
      window.activePopup = popup;
   }

   var popupWrapper = {
      popup,
      openPopup,
      dismissPopup,
      on
   };

   popup.openPopup = openPopup;
   popup.dismissPopup = dismissPopup;

   return popupWrapper;
}

/**
 * Create HTMLElement from HTML string
 * @param {String} htmlString
 * @returns {HTMLElement}
 */
function createElementFromHTML(htmlString) {
   var div = document.createElement("div");
   div.innerHTML = htmlString.trim();
   return div.firstChild;
}

/**
 * @param {{
 * 	id: String,
 * 	trigger: String|null,
 * 	autoOpen: number|null,
 * 	slideIn: Boolean,
 * 	slideFromLeft: Boolean|null,
 * 	title: String|null,
 * 	popupHTML: String | HTMLElement | null
 * }} options
 */
function createPopup(options) {
   if (options.popupHTML instanceof Element) {
      options.popupHTML = options.popupHTML.outerHTML;
   }
   let popupTemplate = `<div class="popup-wrapper ${
      options.slideIn ? "popup-slide-in" : ""
   }"  data-slide-dir="${
      options.slideIn && options.slideFromLeft ? "left" : "right"
   }" id="${
      options.id
   }"><div class="popup-overlay popup-dismiss"></div><div class="popup-container"><button class="popup-close popup-dismiss">&times;</button>${
      options.title ? `<h3 class="popup-title">${options.title}</h3>` : ``
   }<div class="popup-content">${
      options.popupHTML ? options.popupHTML : ""
   }</div></div></div>`;
   let popupElement = createElementFromHTML(popupTemplate);
   document.body.appendChild(popupElement);
   let popup = setUpPopup(popupElement, options.trigger, options.autoOpen);
   return popup;
}
