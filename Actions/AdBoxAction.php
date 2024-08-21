<?php

    namespace Modules\Adboxes\Actions;

    use Modules\AdBoxes\Models\AdBox;

    class AdBoxAction
    {
        public function moveUp($id, $adBox)
        {
            $prevAdBox = AdBox::where('type', $adBox->type)->where('position', $adBox->position - 1)->first();

            if ($prevAdBox) {
                $prevAdBox->increment('position');
                $adBox->decrement('position');
            }
        }

        public function moveDown($id, $adBox)
        {
            $nextAdBox = AdBox::where('type', $adBox->type)->where('position', $adBox->position + 1)->first();

            if ($nextAdBox) {
                $nextAdBox->decrement('position');
                $adBox->increment('position');
            }
        }

        public function updatePosition($id, $newPosition, $adBox): bool
        {
            $currentPosition = $adBox->position;

            if ($newPosition == $currentPosition) {
                return false;
            }

            $adBox->update(['position' => $newPosition]);

            if ($newPosition > $currentPosition) {
                AdBox::where('type', $adBox->type)->whereBetween('position', [$currentPosition + 1, $newPosition])->decrement('position');
            } else {
                AdBox::where('type', $adBox->type)->whereBetween('position', [$newPosition, $currentPosition - 1])->increment('position');
            }

            return true;
        }

        public function returnToWaitingState($adBox)
        {
            if ($adBox->existsFile($adBox->filename)) {
                $adBox->deleteFile($adBox->filename);
            }

            $lastPosition = AdBox::where('type', AdBox::$WAITING_ACTION)->max('position') ? : 0;
            $adBox->update(['type' => AdBox::$WAITING_ACTION, 'position' => $lastPosition + 1]);
        }

        public function deleteImage($adBox)
        {
            if ($adBox->existsFile($adBox->filename)) {
                $adBox->deleteFile($adBox->filename);
            }
        }

        public function decrementPosition($adBoxes)
        {
            foreach ($adBoxes as $adBoxToUpdate) {
                $adBoxToUpdate->update(['position' => $adBoxToUpdate->position - 1]);
            }
        }
    }

