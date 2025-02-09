<?php

namespace App\Ninja\Repositories;

use App\Models\ProposalCategory;
use DB;
use Illuminate\Support\Facades\Auth;

class ProposalCategoryRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'App\Models\ProposalCategory';
    }

    public function all()
    {
        return ProposalCategory::scope()->get();
    }

    public function find($filter = null, $userId = false)
    {
        $query = DB::table('proposal_categories')
            ->where('proposal_categories.account_id', '=', Auth::user()->account_id)
            ->select(
                'proposal_categories.name',
                'proposal_categories.public_id',
                'proposal_categories.user_id',
                'proposal_categories.deleted_at',
                'proposal_categories.is_deleted'
            );

        $this->applyFilters($query, ENTITY_PROPOSAL_CATEGORY);

        if ($filter) {
            $query->where(function ($query) use ($filter): void {
                $query->Where('proposal_categories.name', 'like', '%' . $filter . '%');
            });
        }

        return $query;
    }

    public function save($input, $proposal = false)
    {
        $publicId = $input['public_id'] ?? false;

        if ( ! $proposal) {
            $proposal = ProposalCategory::createNew();
        }

        $proposal->fill($input);
        $proposal->save();

        return $proposal;
    }
}
