/*! Bootstrap integration for DataTables' Buttons
 * ©2016 SpryMedia Ltd - datatables.net/license
 */
!(function (n) {
  "function" == typeof define && define.amd
    ? define(
        ["jquery", "datatables.net-bs4", "datatables.net-buttons"],
        function (t) {
          return n(t, window, document);
        }
      )
    : "object" == typeof exports
    ? (module.exports = function (t, e) {
        return (
          (t = t || window),
          (e =
            e ||
            ("undefined" != typeof window
              ? require("jquery")
              : require("jquery")(t))).fn.dataTable ||
            require("datatables.net-bs4")(t, e),
          e.fn.dataTable || require("datatables.net-buttons")(t, e),
          n(e, 0, t.document)
        );
      })
    : n(jQuery, window, document);
})(function (n, t, e, o) {
  "use strict";
  var a = n.fn.dataTable;
  return (
    n.extend(!0, a.Buttons.defaults, {
      dom: {
        container: { className: "dt-buttons btn-group flex-wrap" },
        button: { className: "btn btn-secondary" },
        collection: {
          tag: "div",
          className: "dropdown-menu",
          closeButton: !1,
          button: {
            tag: "a",
            className: "dt-button dropdown-item",
            active: "active",
            disabled: "disabled",
          },
        },
        splitWrapper: {
          tag: "div",
          className: "dt-btn-split-wrapper btn-group",
          closeButton: !1,
        },
        splitDropdown: {
          tag: "button",
          text: "",
          className:
            "btn btn-secondary dt-btn-split-drop dropdown-toggle dropdown-toggle-split",
          closeButton: !1,
          align: "split-left",
          splitAlignClass: "dt-button-split-left",
        },
        splitDropdownButton: {
          tag: "button",
          className: "dt-btn-split-drop-button btn btn-secondary",
          closeButton: !1,
        },
      },
      buttonCreated: function (t, e) {
        return t.buttons ? n('<div class="btn-group"/>').append(e) : e;
      },
    }),
    (a.ext.buttons.collection.className += " dropdown-toggle"),
    (a.ext.buttons.collection.rightAlignClassName = "dropdown-menu-right"),
    a
  );
});
