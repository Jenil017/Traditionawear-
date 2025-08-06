#!/usr/bin/env python3
"""
Backend Test Suite for PHP Traditional Wear Rental System
Tests database connectivity, user authentication, product management, and cart functionality
"""

import requests
import sys
import json
from datetime import datetime, timedelta
import time

class PHPAppTester:
    def __init__(self, base_url="http://localhost/app"):
        self.base_url = base_url
        self.session = requests.Session()
        self.tests_run = 0
        self.tests_passed = 0
        self.user_session = None
        self.admin_session = None

    def log_test(self, name, success, message=""):
        """Log test results"""
        self.tests_run += 1
        if success:
            self.tests_passed += 1
            print(f"✅ {name}: PASSED {message}")
        else:
            print(f"❌ {name}: FAILED {message}")
        return success

    def test_database_connectivity(self):
        """Test if database connection is working"""
        try:
            response = self.session.get(f"{self.base_url}/user/index.php", timeout=10)
            success = response.status_code == 200 and "Rameshwar Traditional Wear" in response.text
            return self.log_test("Database Connectivity", success, f"Status: {response.status_code}")
        except Exception as e:
            return self.log_test("Database Connectivity", False, f"Error: {str(e)}")

    def test_user_registration(self):
        """Test user registration functionality"""
        try:
            # Get registration page first
            response = self.session.get(f"{self.base_url}/user/register.php")
            if response.status_code != 200:
                return self.log_test("User Registration Page", False, f"Status: {response.status_code}")

            # Test registration with new user
            timestamp = int(time.time())
            test_data = {
                'name': f'Test User {timestamp}',
                'email': f'testuser{timestamp}@example.com',
                'phone': '9876543210',
                'password': 'TestPass123!',
                'confirm_password': 'TestPass123!'
            }

            response = self.session.post(f"{self.base_url}/user/register.php", data=test_data)
            success = response.status_code == 200 and ("success" in response.text.lower() or "registered" in response.text.lower())
            return self.log_test("User Registration", success, f"Status: {response.status_code}")
        except Exception as e:
            return self.log_test("User Registration", False, f"Error: {str(e)}")

    def test_user_login(self):
        """Test user login functionality"""
        try:
            # Test with existing user credentials
            login_data = {
                'email': 'john@example.com',
                'password': 'password'
            }

            response = self.session.post(f"{self.base_url}/user/login.php", data=login_data)
            success = response.status_code == 200 and ("dashboard" in response.url.lower() or "products" in response.url.lower() or "welcome" in response.text.lower())
            
            if success:
                self.user_session = self.session
            
            return self.log_test("User Login", success, f"Status: {response.status_code}")
        except Exception as e:
            return self.log_test("User Login", False, f"Error: {str(e)}")

    def test_admin_login(self):
        """Test admin login functionality"""
        try:
            admin_session = requests.Session()
            login_data = {
                'email': 'admin@rameshwar.com',
                'password': 'password'
            }

            response = admin_session.post(f"{self.base_url}/admin/login.php", data=login_data)
            success = response.status_code == 200 and ("dashboard" in response.url.lower() or "admin" in response.text.lower())
            
            if success:
                self.admin_session = admin_session
            
            return self.log_test("Admin Login", success, f"Status: {response.status_code}")
        except Exception as e:
            return self.log_test("Admin Login", False, f"Error: {str(e)}")

    def test_products_page(self):
        """Test products catalog page"""
        try:
            response = self.session.get(f"{self.base_url}/user/products.php")
            success = response.status_code == 200 and ("product" in response.text.lower() or "sherwani" in response.text.lower() or "kurta" in response.text.lower())
            return self.log_test("Products Page", success, f"Status: {response.status_code}")
        except Exception as e:
            return self.log_test("Products Page", False, f"Error: {str(e)}")

    def test_product_filters(self):
        """Test product filtering functionality"""
        try:
            # Test category filter
            response = self.session.get(f"{self.base_url}/user/products.php?category=1")
            success = response.status_code == 200
            return self.log_test("Product Filters", success, f"Status: {response.status_code}")
        except Exception as e:
            return self.log_test("Product Filters", False, f"Error: {str(e)}")

    def test_product_details(self):
        """Test individual product page"""
        try:
            response = self.session.get(f"{self.base_url}/user/rent.php?id=1")
            success = response.status_code == 200 and ("rent" in response.text.lower() or "book" in response.text.lower())
            return self.log_test("Product Details", success, f"Status: {response.status_code}")
        except Exception as e:
            return self.log_test("Product Details", False, f"Error: {str(e)}")

    def test_cart_functionality(self):
        """Test cart page and functionality"""
        try:
            response = self.session.get(f"{self.base_url}/user/cart.php")
            success = response.status_code == 200 and ("cart" in response.text.lower() or "checkout" in response.text.lower())
            return self.log_test("Cart Page", success, f"Status: {response.status_code}")
        except Exception as e:
            return self.log_test("Cart Page", False, f"Error: {str(e)}")

    def test_checkout_page(self):
        """Test checkout functionality"""
        try:
            response = self.session.get(f"{self.base_url}/user/checkout.php")
            success = response.status_code == 200 and ("checkout" in response.text.lower() or "order" in response.text.lower())
            return self.log_test("Checkout Page", success, f"Status: {response.status_code}")
        except Exception as e:
            return self.log_test("Checkout Page", False, f"Error: {str(e)}")

    def test_admin_dashboard(self):
        """Test admin dashboard access"""
        if not self.admin_session:
            return self.log_test("Admin Dashboard", False, "Admin not logged in")
        
        try:
            response = self.admin_session.get(f"{self.base_url}/admin/dashboard.php")
            success = response.status_code == 200 and ("dashboard" in response.text.lower() or "admin" in response.text.lower())
            return self.log_test("Admin Dashboard", success, f"Status: {response.status_code}")
        except Exception as e:
            return self.log_test("Admin Dashboard", False, f"Error: {str(e)}")

    def test_admin_products_management(self):
        """Test admin products management"""
        if not self.admin_session:
            return self.log_test("Admin Products Management", False, "Admin not logged in")
        
        try:
            response = self.admin_session.get(f"{self.base_url}/admin/manage-products.php")
            success = response.status_code == 200 and ("product" in response.text.lower() or "manage" in response.text.lower())
            return self.log_test("Admin Products Management", success, f"Status: {response.status_code}")
        except Exception as e:
            return self.log_test("Admin Products Management", False, f"Error: {str(e)}")

    def test_api_endpoints(self):
        """Test API endpoints"""
        try:
            # Test filter handler
            response = self.session.get(f"{self.base_url}/api/filter-handler.php?category=1")
            success = response.status_code == 200
            self.log_test("API Filter Handler", success, f"Status: {response.status_code}")

            # Test product details API
            response = self.session.get(f"{self.base_url}/api/product-details.php?id=1")
            success = response.status_code == 200
            self.log_test("API Product Details", success, f"Status: {response.status_code}")

            return True
        except Exception as e:
            return self.log_test("API Endpoints", False, f"Error: {str(e)}")

    def run_all_tests(self):
        """Run all tests in sequence"""
        print("🚀 Starting PHP Traditional Wear Rental System Tests")
        print("=" * 60)

        # Core functionality tests
        self.test_database_connectivity()
        self.test_user_registration()
        self.test_user_login()
        self.test_admin_login()
        
        # Product and catalog tests
        self.test_products_page()
        self.test_product_filters()
        self.test_product_details()
        
        # Cart and checkout tests
        self.test_cart_functionality()
        self.test_checkout_page()
        
        # Admin functionality tests
        self.test_admin_dashboard()
        self.test_admin_products_management()
        
        # API tests
        self.test_api_endpoints()

        # Print summary
        print("\n" + "=" * 60)
        print(f"📊 Test Results: {self.tests_passed}/{self.tests_run} tests passed")
        
        if self.tests_passed == self.tests_run:
            print("🎉 All tests passed! Application is working correctly.")
            return 0
        else:
            print(f"⚠️  {self.tests_run - self.tests_passed} tests failed. Please check the issues above.")
            return 1

def main():
    """Main test execution"""
    tester = PHPAppTester()
    return tester.run_all_tests()

if __name__ == "__main__":
    sys.exit(main())