/** global: Craft */
/** global: Garnish */
/** global: $ */
// noinspection JSVoidFunctionReturnValueUsed

if (typeof Craft.Redirects === typeof undefined) {
  Craft.Redirects = {};
}

(function ($) {
  Craft.Redirects.RedirectLinkedElement = Garnish.Base.extend(
    {
      $container: null,

      init: function (container, settings) {
        this.$container = $(container);
        this.setSettings(
          settings,
          Craft.Redirects.RedirectLinkedElement.defaults
        );

        this.$unlinkElementBtn = this.$container.find(
          "[data-unlink-element-btn]"
        );

        this.$destinationElementId = this.$container.find(
          "[name='destinationElementId']"
        );

        this.addListener(this.$unlinkElementBtn, "click", (ev) => {
          ev.preventDefault();
          this.$destinationElementId.val("");
        });

        // this.changeSite(this.$siteSelect[0].value);

        this.trigger("afterInit");
      },
    },
    {
      defaults: {},
    }
  );
})(jQuery);
