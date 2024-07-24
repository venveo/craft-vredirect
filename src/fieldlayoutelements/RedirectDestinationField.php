<?php

namespace venveo\redirect\fieldlayoutelements;

use Craft;
use craft\base\ElementInterface;
use craft\fieldlayoutelements\BaseField;
use craft\helpers\Cp;
use craft\helpers\Html;
use venveo\redirect\elements\Redirect;
use venveo\redirect\Plugin;
use venveo\redirect\web\assets\redirectscp\RedirectsCpAsset;
use yii\base\InvalidArgumentException;

class RedirectDestinationField extends BaseField
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

        $config = [
            'id' => 'redirectDestination',
        ];
        $destinationSites = Plugin::getInstance()->redirects->getSiteSelectorOptions();
        $config['siteOptions'] = $destinationSites;


        $view = Craft::$app->getView();

        $view->registerJsWithVars(fn($namespace, $settings) => <<<JS
    const container = $('#' + Craft.namespaceId('redirectDestination', $namespace));
new Craft.Redirects.UrlFieldInput(container, $settings);
JS, [
            $view->getNamespace(),
            [
                'siteOptions' => $config['siteOptions'],
            ],
        ]);

        $siteOptions = array_merge([['label' => 'External URL', 'value' => null]], array_map(static function ($site) {
            return ['label' => $site['name'], 'value' => $site['id'], 'disabled' => !$site['editable']];
        }, $destinationSites));

        $html = Html::beginTag('div', ['id' => $config['id']]) .
            Cp::selectHtml([
                'class' => 'sites',
                'options' => $siteOptions,
                'name' => 'destinationSiteId',
                'value' => $element->destinationSiteId,
            ]) .
            Html::beginTag('div', ['class' => 'destinationUrlWrapper']) .
            Html::tag('div', '', [
                'class' => 'prefix',
            ]) .
            Cp::textFieldHtml([
                'class' => 'url',
                'name' => 'destinationUrl',
                'value' => $element->destinationUrl,
            ]) .
            Html::endTag('div') . // inner div
            Html::endTag('div'); // Outer Div
        return $html;
    }

    public function attribute(): string
    {
        return 'redirectDestination';
    }
}
