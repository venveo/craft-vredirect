/** global: Craft */
/** global: Garnish */
/** global: $ */
// noinspection JSVoidFunctionReturnValueUsed

if (typeof Craft.Redirects === typeof undefined) {
  Craft.Redirects = {};
}

(function ($) {
  Craft.Redirects.LinkDestinationToElementTip = Garnish.Base.extend(
    {
      $container: null,

      init: function (container, settings) {
        this.$container = $(container);
        this.setSettings(
          settings,
          Craft.Redirects.LinkDestinationToElementTip.defaults
        );
        const suggestedElementId = this.settings.suggestedElementId;
        this.$destinationElementIdContainer = this.$container.find(
          "[name='destinationElementId']"
        );

        this.$linkButton = this.$container.find("button");

        this.addListener(this.$linkButton, "click", (ev) => {
          ev.preventDefault();
          this.$destinationElementIdContainer.val(suggestedElementId);
        });

        this.trigger("afterInit");
      },
    },
    {
      defaults: {},
    }
  );
})(jQuery);
