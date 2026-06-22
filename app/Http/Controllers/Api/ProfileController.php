<?php

namespace App\Http\Controllers\Api;

use App\Models\Member;
use App\Models\MemberFamily;
use App\Models\MemberVehicle;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|nullable|email',
            'phone' => 'sometimes|string|max:20',
        ]);
        $user->update($data);

        return $this->ok(['user' => $user->fresh()], 'Profile updated');
    }

    public function updateMember(Request $request)
    {
        $member = $request->user()->member;
        if (! $member) {
            return $this->fail('No resident profile linked.', 404);
        }

        $data = $request->validate([
            'id_proof_type' => 'nullable|string',
            'id_proof_no' => 'nullable|string',
        ]);
        $member->update($data);

        return $this->ok($member->fresh()->load(['family', 'vehicles', 'flat']));
    }

    public function addFamily(Request $request)
    {
        $member = $request->user()->member;
        if (! $member) {
            return $this->fail('No member profile.', 404);
        }

        $data = $request->validate([
            'name' => 'required|string',
            'relation' => 'required|string',
            'phone' => 'nullable|string',
        ]);

        $family = MemberFamily::create([...$data, 'member_id' => $member->id]);

        return $this->ok($family, 'Family member added');
    }

    public function addVehicle(Request $request)
    {
        $member = $request->user()->member;
        if (! $member) {
            return $this->fail('No member profile.', 404);
        }

        $data = $request->validate([
            'vehicle_no' => 'required|string',
            'type' => 'required|in:car,bike,other',
        ]);

        $vehicle = MemberVehicle::create([
            ...$data,
            'member_id' => $member->id,
            'society_id' => $member->society_id,
        ]);

        return $this->ok($vehicle, 'Vehicle added');
    }
}
