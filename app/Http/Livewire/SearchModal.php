<?php

namespace App\Http\Livewire;

use Algolia\AlgoliaSearch\SearchIndex;
use App\Models\Lesson;
use Livewire\Component;

class SearchModal extends Component
{
    public $input = '';
    public $results = [];
    public $queryResult = '';

    public function updatedInput()
    {
        if (strlen($this->input) > 1) {
            $this->results = Lesson::search(
                $this->input,
                function (SearchIndex $algolia, string $query, array $options) {
                    $options['highlightPreTag'] = '<span class="search-hits">';
                    $options['highlightPostTag'] = '</span>';
                    $options['hitsPerPage'] = 20;
                    return $algolia->search($query, $options);
                }
            )->raw()['hits'];
            $this->queryResult = $this->input;
        } else {
            $this->results = [];
            $this->queryResult = $this->input;
        }
    }

    public function resetInput()
    {
        $this->input = '';
        $this->results = [];
        $this->queryResult = '';
    }

    public function render()
    {
        return view('livewire.search-modal');
    }
}