# Lizards and Pumpkins Key Value Store Library

As most of Lizards and Pumpkins "technology stub" libraries it consists of 2 implementations. An "in-memory"
implementation and "file" implementation.

An "in-memory" implementation of key-value store is only intended to be used for integration tests. It it holds all
pairs in an associative array so it is vanished once the script is destructed. Why do you need it? Simply because you
can't beat the speed of memory, it is as simple as possible and the most important it is technology agnostic. Still it
implements the `KeyValueStore` interface and fully functional which makes it perfect match for integration tests of
each other component which requires a key-value store.

The second implementation is a filesystem based key-value store. To contrast with "in-memory" key-value store a
filesystem based key-value store might be the most slowest implementation one can imagine. However this is definitely a
most visual one which makes it perfect for development process. Keys are used for file names and values for a content
of a file. Again it implements the high level policy of `KeyValueStore` interface which means you can seamlessly
replace it with a high performance implementation for your productive environment.

## Installation

Lizards and Pumpkins Key Value Store Library can be installed either via composer by just adding it to list of
requirements:

```json
"require": {
    "lizards-and-pumpkins/lib-key-value-store": "dev-master"
}
```

assuming that "packages.lizardsandpumpkins.com" is listed among repositories of your project:

```json
"repositories": [
    {
        "type": "composer",
        "url": "http://packages.lizardsandpumpkins.com"
    }
]
```

Or it can be cloned as any other git repository and put into a place where auto-loader of your project will be able to
pick it up.

## Usage

An instance of "in-memory" key-value store can be obtained by just creating a new object of `InMemoryKeyValueStore`
class in an appropriate method of an appropriate factory of your project. Then all methods of `KeyValueStore`
interface are applied.

The constructor of a filesystem based key-value store implementation has a single argument which is mandatory and
which is supposed to be a path to an existing directory which should be used for storing data files. Then again any
methods of `KeyValueStore` interface can be used.
