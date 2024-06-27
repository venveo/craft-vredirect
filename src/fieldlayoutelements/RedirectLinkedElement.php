<?php

namespace venveo\redirect\fieldlayoutelements;

use Craft;
use craft\base\ElementInterface;
use craft\fieldlayoutelements\BaseUiElement;
use craft\helpers\Cp;
use craft\helpers\Html;
use venveo\redirect\elements\Redirect;
use venveo\redirect\Plugin;
use venveo\redirect\web\assets\redirectscp\RedirectsCpAsset;
use yii\base\InvalidArgumentException;

class RedirectLinkedElement extends BaseUiElement
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


    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
    }

    /**
     * @param Redirect|null $element
     * @param bool $static
     * @return string|null
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \yii\base\Exception
     */
    protected function inputHtml(?ElementInterface $element = null, bool $static = false): ?string
    {
        if (!$element instanceof Redirect) {
            throw new InvalidArgumentException('RedirectDestinationField can only be used in Redirect field layouts.');
        }
        Craft::$app->getView()->registerAssetBundle(RedirectsCpAsset::class);
        $html = Html::hiddenInput('destinationElementId', null);
        $html .= Html::tag('div', 'Destination element id', [
            'class' => 'destinationElement',
        ]);
        return $html;
    }

    public function attribute(): string
    {
        return 'destinationElementId';
    }

    /**
     * @param Redirect $element
     * @return bool
     */
    public function showInForm(?ElementInterface $element = null): bool
    {
        return $element instanceof Redirect && $element->destinationElementId !== null;
    }

    public function formHtml(?ElementInterface $element = null, bool $static = false): ?string
    {
            if (!$element instanceof Redirect) {
                throw new InvalidArgumentException('RedirectDestinationField can only be used in Redirect field layouts.');
            }
            Craft::$app->getView()->registerAssetBundle(RedirectsCpAsset::class);

            $config = [
                'id' => 'redirectLinkedElement',
            ];
            $view = Craft::$app->getView();

            $view->registerJsWithVars(fn($namespace, $settings) => <<<JS
new Craft.Redirects.RedirectLinkedElement($('#' + Craft.namespaceId('redirectLinkedElement', $namespace)), $settings);
JS, [
                $view->getNamespace(),
                [
                ],
            ]);

            $unlinkButton = Html::button('Unlink Element', [
                'class' => 'btn',
                'title' => Craft::t('vredirect', 'Unlink Element'),
                'aria' => [
                    'label' => Craft::t('vredirect', 'Unlink Element'),
                ],
                'data' => [
                    'icon' => 'remove',
                    'unlink-element-btn' => '',
                ],
            ]);
            $html = Html::beginTag('div', ['id' => $config['id']]) .
                Html::tag('p', Plugin::t('This redirect is connected to an element. If the connected element\'s URL changes, the redirect will update automatically. If the linked element is deleted, the redirect will use the original URL value supplied.')) .
                Html::beginTag('div', ['class' => 'flex', 'style' => 'padding-top: 10px;']) .
                Cp::elementSelectHtml([
                    'disabled' => true,
                    'name' => 'destinationElementId',
                    'single' => true,
                    'elements' => [$element->destinationElement],
                ]) .
                $unlinkButton .

                Html::endTag('div') .
                Html::endTag('div');
            return $html;
    }

    protected function selectorLabel(): string
    {
        return 'Destination Element ID';
    }
}
