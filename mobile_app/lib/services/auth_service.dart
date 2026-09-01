import '../config/api_constants.dart';
import 'api_service.dart';
import 'storage_service.dart';

class AuthService {
  static Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await ApiService.post(
      '${ApiConstants.auth}/login',
      body: {'email': email, 'password': password},
    );
    if (response['token'] != null) {
      await StorageService.saveToken(response['token']);
      await StorageService.saveUser(response);
    }
    return response;
  }

  static Future<void> logout() async {
    await StorageService.clear();
  }

  static Future<Map<String, dynamic>?> getCurrentUser() async {
    return await StorageService.getUser();
  }

  static Future<bool> isLoggedIn() async {
    return await StorageService.isLoggedIn();
  }
}
