<?php

namespace venveo\redirect\fieldlayoutelements;

use Craft;
use craft\base\ElementInterface;
use craft\fieldlayoutelements\BaseUiElement;
use craft\helpers\Html;
use venveo\redirect\elements\Redirect;
use venveo\redirect\Plugin;
use venveo\redirect\web\assets\redirectscp\RedirectsCpAsset;
use yii\base\InvalidArgumentException;

class LinkDestinationToElementTip extends BaseUiElement
{

    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        unset(
            $config['attribute'],
            $config['mandatory'],
            $config['requirable'],
            $config['translatable'],
        );

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        $fields = parent::fields();
        unset(
            $fields['mandatory'],
            $fields['translatable'],
        );
        return $fields;
    }

    public function attribute(): string
    {
        return 'destinationElementId';
    }



    public ElementInterface|null $suggestedElement;


    protected function selectorLabel(): string
    {
        return '';
    }

    /**
     * @param Redirect $element
     * @return bool
     */
    public function showInForm(?ElementInterface $element = null): bool
    {
        return $element instanceof Redirect && $element->destinationElementId === null;
    }


    public function formHtml(?ElementInterface $element = null, bool $static = false): ?string
    {
        if (!$element instanceof Redirect) {
            throw new InvalidArgumentException('LinkDestinationToElementTip can only be used in Redirect field layouts.');
        }

        Craft::$app->getView()->registerAssetBundle(RedirectsCpAsset::class);

        $config = [
            'id' => 'destinationElementId',
        ];
        $view = Craft::$app->getView();

        $view->registerJsWithVars(fn($namespace, $settings) => <<<JS
new Craft.Redirects.LinkDestinationToElementTip($('#' + Craft.namespaceId('destinationElementId', $namespace)), $settings);
JS, [
            $view->getNamespace(),
            [
                'suggestedElementId' => $this->suggestedElement->id,
            ],
        ]);

        $message = Plugin::t('This looks like an internal link. Would you like to link this redirect to the element to keep them in sync?');
        $button = Html::button('Yes, connect them.', [
            'class' => 'btn',
        ]);
        $html = Html::beginTag('div', ['id' => $config['id']]);
        $html .= Html::hiddenInput('destinationElementId', null);
        $html .= "<div class=\"readable\">" .
            "<blockquote class=\"note tip\">" .
            $message .
            '<br><br>' .
            $button .
            "</blockquote>" .
            '</div>';
        $html .= Html::endTag('div');
        return $html;
    }
}
