import requests
import concurrent.futures
import time

# Function to send a single HTTP request with bearer token
def send_request(url, token):
    try:
        headers = {
            "Authorization": f"Bearer {token}"
        }
        response = requests.get(url, headers=headers)
        return response.status_code
    except requests.exceptions.RequestException as e:
        return str(e)

# Function to send multiple requests
def send_requests(url, token, num_requests):
    with concurrent.futures.ThreadPoolExecutor(max_workers=num_requests) as executor:
        start_time = time.time()
        futures = [executor.submit(send_request, url, token) for _ in range(num_requests)]
        for future in concurrent.futures.as_completed(futures):
            print(future.result())
        end_time = time.time()
    total_time = end_time - start_time

    print(f"Total time taken: {total_time} seconds")

if __name__ == "__main__":
    # Configure your settings here
    url = "http://localhost:8000/api/list-branches/4"  # URL to send requests to
    token = "1|c9s8MyMyf8JydxktkgoS6AyjqA1dB9OmrfHJcZtge98689e1"  # Bearer token
    num_requests = 100  # Number of requests to send in total

    send_requests(url, token, num_requests)
