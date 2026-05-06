<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Dependant;

class DependantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'dob' => $this->date_of_birth,
            'schoolName' => $this->school_name,
            'grade' => $this->grade,
            'gender' => $this->gender,
            'profileImageUrl' => $this->avatar ? asset('storage/' . $this->avatar): null,

            'bloodGroup' => $this->blood_group,
            'allergies' => $this->allergies,
            'primaryDoctor' => $this->doctor_contact,
            'insuranceInfo' => $this->insurance_provider,
            'devices' => $this->tag ? [$this->tag] : [],
            'events' =>$this->events ?? [],
        ];
    }
}
