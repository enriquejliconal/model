<?php

namespace Orchestra\Model\Observer;

use InvalidArgumentException;
use Orchestra\Contracts\Authorization\Factory;
use Orchestra\Model\Role as Eloquent;
use Orchestra\Support\Keyword;

class Role
{
    /**
     * The authorization factory implementation.
     *
     * @var \Orchestra\Contracts\Authorization\Factory
     */
    protected $acl;

    /**
     * Construct a new role observer.
     */
    public function __construct(Factory $acl)
    {
        $this->acl = $acl;
    }

    /**
     * On creating observer.
     */
    public function creating(Eloquent $model): void
    {
        $this->acl->addRole($model->getAttribute('name'));
    }

    /**
     * On saving observer.
     */
    public function saving(Eloquent $model): void
    {
        $keyword = Keyword::make($model->getAttribute('name'));

        if ($keyword->searchIn(['guest']) !== false) {
            throw new InvalidArgumentException("Role [{$keyword->getValue()}] is not allowed to be used!");
        }
    }

    /**
     * On deleting observer.
     */
    public function deleting(Eloquent $model): void
    {
        $this->acl->removeRole($model->getAttribute('name'));
    }

    /**
     * On updating/restoring observer.
     */
    public function updating(Eloquent $model): void
    {
        $original = $model->getOriginal('name');
        $current = $model->getAttribute('name');

        if ($this->isRestoringModel($model)) {
            $this->acl->addRole($current);
        } else {
            $this->acl->renameRole($original, $current);
        }
    }

    /**
     * Is restoring model.
     */
    protected function isRestoringModel(Eloquent $model): bool
    {
        if (! $model->isSoftDeleting()) {
            return false;
        }

        $deleted = $model->getDeletedAtColumn();

        return \is_null($model->getAttribute($deleted)) && ! \is_null($model->getOriginal($deleted));
    }
}
