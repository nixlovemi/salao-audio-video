<?php

namespace App\Tables;

use App\Models\People;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Column;
use Okipa\LaravelTable\Table;
use Okipa\LaravelTable\Filters\ValueFilter;
use Okipa\LaravelTable\Formatters\BooleanFormatter;
use App\Tables\RowActions\ActivateRowAction;
use App\Tables\RowActions\DeactivateRowAction;
use Okipa\LaravelTable\RowActions\ShowRowAction;
use Okipa\LaravelTable\RowActions\EditRowAction;

class PeoplesTable extends AbstractTableConfiguration
{
    protected function table(): Table
    {
        return Table::make()
            ->model(People::class)
            ->numberOfRowsPerPageOptions([15])
            ->rowActions(fn(People $People) => [
                (new ActivateRowAction('active'))
                    ->when(!$People->active)
                    ->confirmationQuestion('Deseja marcar como ativa a pessoa `' . $People->name . '`?')
                    ->feedbackMessage(false),
                (new DeactivateRowAction('active'))
                    ->when($People->active)
                    ->confirmationQuestion('Deseja marcar como inativa a pessoa `' . $People->name . '`?')
                    ->feedbackMessage(false),
                (new ShowRowAction(route('people.view', ['codedId' => $People->codedId])))
                    ->when(true),
                (new EditRowAction(route('people.edit', ['codedId' => $People->codedId])))
                    ->when($People->active == 1),
            ])
            ->filters([
                new ValueFilter(
                    'Ativo (Todos):',
                    'active',
                    [
                        1 => 'Sim',
                        0 => 'Não',
                    ],
                    false
                ),
            ]);;
    }

    protected function columns(): array
    {
        return [
            Column::make('name')->title('Nome')->sortable()->searchable(),
            Column::make('active')->title('Ativo')->format(new BooleanFormatter()),
        ];
    }
}
