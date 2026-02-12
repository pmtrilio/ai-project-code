 */

namespace Symfony\Component\Translation;

use Symfony\Component\Config\Resource\ResourceInterface;
use Symfony\Component\Translation\Exception\LogicException;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MessageCatalogue implements MessageCatalogueInterface, MetadataAwareInterface, CatalogueMetadataAwareInterface
{
    private array $messages = [];
    private array $metadata = [];
    private array $catalogueMetadata = [];
    private array $resources = [];
    private string $locale;
    private ?MessageCatalogueInterface $fallbackCatalogue = null;
    private ?self $parent = null;

    /**
     * @param array $messages An array of messages classified by domain
     */
    public function __construct(string $locale, array $messages = [])
    {
        $this->locale = $locale;
        $this->messages = $messages;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getDomains(): array
    {
        $domains = [];

        foreach ($this->messages as $domain => $messages) {
            if (str_ends_with($domain, self::INTL_DOMAIN_SUFFIX)) {
                $domain = substr($domain, 0, -\strlen(self::INTL_DOMAIN_SUFFIX));