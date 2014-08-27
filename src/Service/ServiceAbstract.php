<?php
namespace Grav\Plugin\VideoEmbed\Service;

abstract class ServiceAbstract implements ServiceInterface
{
    protected $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Process html
     * @param string $html
     * @return string
     */
    public function processHtml($html)
    {
        $urlRegExpr = $this->getRegExpression();
        $linkRegexp = "/<a href=\"$urlRegExpr\">$urlRegExpr<\/a>/i";

        $document = new \DOMDocument();
        $containerNode = $this->createDOMNode(
            $document,
            'div',
            (array)$this->config('container_html_attr', [])
        );

        return preg_replace_callback($linkRegexp, function ($matches) use ($document, $containerNode) {
            $frameNode = $this->getEmbedNode($matches);
            if ($containerNode->hasChildNodes()) {
                $containerNode->replaceChild($frameNode, $containerNode->firstChild);
            } else {
                $containerNode->appendChild($frameNode);
            }

            return html_entity_decode(
                $document->saveHTML($frameNode->parentNode)
            );
        }, $html);
    }

    /**
     * Service specific config
     * @param $item
     * @param null $default
     * @return null
     */
    protected function config($item, $default = null)
    {
        if (empty($this->config[$item])) {
            return $default;
        }

        return $this->config[$item];
    }

    /**
     * Set many attributes for node, e.g. [src => google.com, class => video]
     * @param $setter \Callable
     * @param array $attributes
     * @throws \InvalidArgumentException
     */
    protected function batchSetAttributes($setter, array $attributes)
    {
        if (!is_callable($setter)) {
            throw new \InvalidArgumentException('Func must be callable');
        }

        foreach ($attributes as $attr => $value) {
            call_user_func($setter, $attr, $value);
        }
    }

    /**
     * Create node with passed attributes
     * @param $doc \DOMDocument
     * @param $name string dom node name
     * @param array $attributes
     * @return \DOMElement
     */
    protected function createDOMNode(\DOMDocument $doc, $name, array $attributes = [])
    {
        $node = $doc->createElement($name);
        $this->batchSetAttributes(
            [$node, 'setAttribute'],
            $attributes
        );

        return $node;
    }
}
