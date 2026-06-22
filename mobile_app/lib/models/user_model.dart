class UserModel {
  UserModel({
    required this.id,
    required this.name,
    required this.email,
    required this.phone,
    required this.role,
    required this.roleLabel,
    this.societyName,
    this.flat,
    this.locale = 'en',
    this.darkMode = false,
    this.memberId,
    this.memberType,
    this.family = const [],
    this.vehicles = const [],
    this.idProofType,
    this.idProofNo,
  });

  final int id;
  final String name;
  final String email;
  final String phone;
  final String role;
  final String roleLabel;
  final String? societyName;
  final String? flat;
  final String locale;
  final bool darkMode;
  final int? memberId;
  final String? memberType;
  final List<Map<String, dynamic>> family;
  final List<Map<String, dynamic>> vehicles;
  final String? idProofType;
  final String? idProofNo;

  factory UserModel.fromJson(Map<String, dynamic> j) {
    final society = j['society'] as Map<String, dynamic>?;
    final member = j['member'] as Map<String, dynamic>?;
    final prefs = j['preferences'] as Map<String, dynamic>?;

    List<Map<String, dynamic>> asList(dynamic v) {
      if (v is List) return v.map((e) => Map<String, dynamic>.from(e as Map)).toList();
      return [];
    }

    return UserModel(
      id: j['id'] as int? ?? 0,
      name: j['name']?.toString() ?? '',
      email: j['email']?.toString() ?? '',
      phone: j['phone']?.toString() ?? '',
      role: j['role']?.toString() ?? '',
      roleLabel: j['role_label']?.toString() ?? j['role']?.toString() ?? '',
      societyName: society?['name']?.toString(),
      flat: member?['flat']?.toString(),
      locale: prefs?['locale']?.toString() ?? 'en',
      darkMode: prefs?['dark_mode'] == true,
      memberId: member?['id'] as int?,
      memberType: member?['member_type']?.toString(),
      family: asList(member?['family']),
      vehicles: asList(member?['vehicles']),
      idProofType: member?['id_proof_type']?.toString(),
      idProofNo: member?['id_proof_no']?.toString(),
    );
  }
}
