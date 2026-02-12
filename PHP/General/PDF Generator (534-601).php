     * @param DOMNode $node
     * @deprecated
     */
    public static function remove_text_nodes(DOMNode $node)
    {
        self::removeTextNodes($node);
    }

    /**
     * @param DOMNode $node
     */
    public static function removeTextNodes(DOMNode $node)
    {
        $children = [];
        for ($i = 0; $i < $node->childNodes->length; $i++) {
            $child = $node->childNodes->item($i);
            if ($child->nodeName === "#text") {
                $children[] = $child;
            }
        }

        foreach ($children as $child) {
            $node->removeChild($child);
        }
    }

    /**
     * Builds the {@link FrameTree}, loads any CSS and applies the styles to
     * the {@link FrameTree}
     */
    private function processHtml()
    {
        $this->tree->build_tree();

        $this->css->load_css_file($this->css->getDefaultStylesheet(), Stylesheet::ORIG_UA);

        $acceptedmedia = Stylesheet::$ACCEPTED_GENERIC_MEDIA_TYPES;
        $acceptedmedia[] = $this->options->getDefaultMediaType();

        // <base href="" />
        /** @var \DOMElement|null */
        $baseNode = $this->dom->getElementsByTagName("base")->item(0);
        $baseHref = $baseNode ? $baseNode->getAttribute("href") : "";
        if ($baseHref !== "") {
            [$this->protocol, $this->baseHost, $this->basePath] = Helpers::explode_url($baseHref);
        }

        // Set the base path of the Stylesheet to that of the file being processed
        $this->css->set_protocol($this->protocol);
        $this->css->set_host($this->baseHost);
        $this->css->set_base_path($this->basePath);

        // Get all the stylesheets so that they are processed in document order
        $xpath = new DOMXPath($this->dom);
        $stylesheets = $xpath->query("//*[name() = 'link' or name() = 'style']");

        /** @var \DOMElement $tag */
        foreach ($stylesheets as $tag) {
            switch (strtolower($tag->nodeName)) {
                // load <link rel="STYLESHEET" ... /> tags
                case "link":
                    if (
                        (stripos($tag->getAttribute("rel"), "stylesheet") !== false // may be "appendix stylesheet"
                        || mb_strtolower($tag->getAttribute("type")) === "text/css")
                        && stripos($tag->getAttribute("rel"), "alternate") === false // don't load "alternate stylesheet"
                    ) {
                        //Check if the css file is for an accepted media type
                        //media not given then always valid