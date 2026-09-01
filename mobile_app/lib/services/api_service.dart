import 'dart:convert';
import 'package:http/http.dart' as http;
import 'storage_service.dart';

class ApiService {
  static Future<Map<String, String>> _headers() async {
    final token = await StorageService.getToken();
    return {
      'Content-Type': 'application/json',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  static Future<Map<String, dynamic>> get(String url) async {
    final response = await http.get(
      Uri.parse(url),
      headers: await _headers(),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> post(String url, {dynamic body}) async {
    final response = await http.post(
      Uri.parse(url),
      headers: await _headers(),
      body: body != null ? jsonEncode(body) : null,
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> put(String url, {dynamic body}) async {
    final response = await http.put(
      Uri.parse(url),
      headers: await _headers(),
      body: body != null ? jsonEncode(body) : null,
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> delete(String url) async {
    final response = await http.delete(
      Uri.parse(url),
      headers: await _headers(),
    );
    return _handleResponse(response);
  }

  static Future<Map<String, dynamic>> postMultipart(String url, {Map<String, String>? fields, String? filePath, String? fileField}) async {
    final token = await StorageService.getToken();
    final request = http.MultipartRequest('POST', Uri.parse(url));
    if (token != null) request.headers['Authorization'] = 'Bearer $token';
    if (fields != null) request.fields.addAll(fields);
    if (filePath != null && fileField != null) {
      request.files.add(await http.MultipartFile.fromPath(fileField, filePath));
    }
    final streamedResponse = await request.send();
    final response = await http.Response.fromStream(streamedResponse);
    return _handleResponse(response);
  }

  static Map<String, dynamic> _handleResponse(http.Response response) {
    final body = jsonDecode(response.body);
    if (response.statusCode >= 200 && response.statusCode < 300) {
      return body is Map<String, dynamic> ? body : {'data': body};
    }
    throw Exception(body['message'] ?? body['error'] ?? 'Erreur ${response.statusCode}');
  }
}
