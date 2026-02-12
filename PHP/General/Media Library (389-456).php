        return $this->responsiveImages($conversionName)->getUrls();
    }

    public function hasResponsiveImages(string $conversionName = ''): bool
    {
        return count($this->getResponsiveImageUrls($conversionName)) > 0;
    }

    public function getSrcset(string $conversionName = ''): string
    {
        return $this->responsiveImages($conversionName)->getSrcset();
    }

    protected function previewUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->hasGeneratedConversion('preview') ? $this->getUrl('preview') : '',
        );
    }

    protected function originalUrl(): Attribute
    {
        return Attribute::get(fn () => $this->getUrl());
    }

    /** @param  string  $collectionName */
    public function move(HasMedia $model, $collectionName = 'default', string $diskName = '', string $fileName = ''): self
    {
        $newMedia = $this->copy($model, $collectionName, $diskName, $fileName);

        $this->forceDelete();

        return $newMedia;
    }

    /**
     * @param  null|Closure(FileAdder): FileAdder  $fileAdderCallback
     */
    public function copy(
        HasMedia $model,
        string $collectionName = 'default',
        string $diskName = '',
        string $fileName = '',
        ?Closure $fileAdderCallback = null
    ): self {
        $temporaryDirectory = TemporaryDirectory::create();

        $temporaryFile = $temporaryDirectory->path('/').DIRECTORY_SEPARATOR.$this->file_name;

        /** @var Filesystem $filesystem */
        $filesystem = app(Filesystem::class);

        $filesystem->copyFromMediaLibrary($this, $temporaryFile);

        $fileAdder = $model
            ->addMedia($temporaryFile)
            ->usingName($this->name)
            ->setOrder($this->order_column)
            ->withManipulations($this->manipulations)
            ->withCustomProperties($this->custom_properties);

        if ($fileName !== '') {
            $fileAdder->usingFileName($fileName);
        }

        if ($fileAdderCallback instanceof Closure) {
            $fileAdder = $fileAdderCallback($fileAdder);
        }