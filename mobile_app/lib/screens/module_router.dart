import 'package:flutter/material.dart';

import 'modules/module_screens.dart';

class ModuleRouter {
  static Widget build(String route, String role) {
    return ModuleScreens.build(route, role);
  }

  static String titleFor(String route) {
    return ModuleScreens.titleFor(route);
  }
}
