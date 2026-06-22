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

  factory UserModel.fromJson(Map<String, dynamic> j) {
    final society = j['society'] as Map<String, dynamic>?;
    final member = j['member'] as Map<String, dynamic>?;
    final prefs = j['preferences'] as Map<String, dynamic>?;

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
    );
  }
}
