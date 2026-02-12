    const TYPE_UPC_A = 'UPCA';
    const TYPE_UPC_E = 'UPCE';
    const TYPE_MSI = 'MSI'; // MSI (Variation of Plessey code)
    const TYPE_MSI_CHECKSUM = 'MSI+'; // MSI + CHECKSUM (modulo 11)
    const TYPE_POSTNET = 'POSTNET';
    const TYPE_PLANET = 'PLANET';
    const TYPE_TELEPEN_ALPHA = 'TELEPENALPHA';
    const TYPE_TELEPEN_NUMERIC = 'TELEPENNUMERIC';
    const TYPE_RMS4CC = 'RMS4CC'; // RMS4CC (Royal Mail 4-state Customer Code) - CBC (Customer Bar Code)
    const TYPE_KIX = 'KIX'; // KIX (Klant index - Customer index)
    const TYPE_IMB = 'IMB'; // IMB - Intelligent Mail Barcode - Onecode - USPS-B-3200
    const TYPE_CODABAR = 'CODABAR';
    const TYPE_CODE_11 = 'CODE11';
    const TYPE_PHARMA_CODE = 'PHARMA';
    const TYPE_PHARMA_CODE_TWO_TRACKS = 'PHARMA2T';

    /**
     * @throws UnknownTypeException
     */
    protected function getBarcodeData(string $code, string $type): Barcode
    {
        $barcodeDataBuilder = $this->createDataBuilderForType($type);

        return $barcodeDataBuilder->getBarcode($code);
    }

    protected function createDataBuilderForType(string $type): TypeInterface
    {
        switch (strtoupper($type)) {
            case self::TYPE_CODE_32:
                return new TypeCode32();
                
            case self::TYPE_CODE_39:
                return new TypeCode39();

            case self::TYPE_CODE_39_CHECKSUM:
                return new TypeCode39Checksum();

            case self::TYPE_CODE_39E:
                return new TypeCode39Extended();

            case self::TYPE_CODE_39E_CHECKSUM:
                return new TypeCode39ExtendedChecksum();

            case self::TYPE_CODE_93:
                return new TypeCode93();

            case self::TYPE_STANDARD_2_5:
                return new TypeStandard2of5();

            case self::TYPE_STANDARD_2_5_CHECKSUM:
                return new TypeStandard2of5Checksum();

            case self::TYPE_INTERLEAVED_2_5:
                return new TypeInterleaved25();

            case self::TYPE_INTERLEAVED_2_5_CHECKSUM: