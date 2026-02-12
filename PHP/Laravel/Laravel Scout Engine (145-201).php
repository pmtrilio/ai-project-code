     */
    public function cursor(Builder $builder)
    {
        return $this->lazyMap(
            $builder, $this->search($builder), $builder->model
        );
    }
}
