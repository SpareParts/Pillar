# Pillar

[![Build Status](https://travis-ci.org/SpareParts/Pillar.svg?branch=master)](https://travis-ci.org/SpareParts/Pillar)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SpareParts/Pillar/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SpareParts/Pillar/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/SpareParts/Pillar/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/SpareParts/Pillar/?branch=master)

````php
$assistant->fluent(GridProduct::class)
	->selectEntityProperties()
	->fromEntityDataSources()
	->setSorting(new Sorting('name', SortingDirection::ASC()))
	->fetchAll();
````
