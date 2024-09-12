<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    //Hashes and updates the record's password if a new one is provided.
    protected function beforeSave(): void
    {
        $formState = $this->form->getState();

        if (!empty($formState['password'])) {
            $this->record->password = Hash::make($formState['password']);
        }
    }
}
