# Pillar

$assistant->fluent(GridProduct::class)
	->selectEntityProperties()
	->fromEntityDataSources()
	->setSorting(new Sorting('name', SortingDirection::ASC()))
	->fetchAll();
