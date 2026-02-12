namespace Laminas\InputFilter;

use Traversable;

use function is_array;

/**
 * @psalm-import-type InputSpecification from InputFilterInterface
 * @template TFilteredValues
 * @extends BaseInputFilter<TFilteredValues>
 */
class InputFilter extends BaseInputFilter
{
    /** @var Factory|null */
    protected $factory;

    /**
     * Set factory to use when adding inputs and filters by spec
     *
     * @return InputFilter
     */
    public function setFactory(Factory $factory)
    {
        $this->factory = $factory;
        return $this;
    }

    /**
     * Get factory to use when adding inputs and filters by spec
     *
     * Lazy-loads a Factory instance if none attached.
     *
     * @return Factory
     */
    public function getFactory()
    {
        if (null === $this->factory) {
            $this->factory = new Factory();
        }
        return $this->factory;
    }