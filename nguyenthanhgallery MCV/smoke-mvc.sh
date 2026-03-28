#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HOST="127.0.0.1"
PORT="${1:-8099}"
BASE_URL="http://${HOST}:${PORT}"
SERVER_LOG="${ROOT_DIR}/.smoke-server.log"

PHP_PID=""
FAILURES=0

cleanup() {
    if [[ -n "${PHP_PID}" ]] && kill -0 "${PHP_PID}" >/dev/null 2>&1; then
        kill "${PHP_PID}" >/dev/null 2>&1 || true
        wait "${PHP_PID}" 2>/dev/null || true
    fi
    if [[ -n "${SERVER_LOG}" && -f "${SERVER_LOG}" ]]; then
        rm -f "${SERVER_LOG}"
    fi
}
trap cleanup EXIT

start_server() {
    SERVER_LOG="$(mktemp -t mvc-smoke.XXXXXX.log)"
    php -S "${HOST}:${PORT}" router.php >"${SERVER_LOG}" 2>&1 &
    PHP_PID=$!
    sleep 1
    if ! kill -0 "${PHP_PID}" >/dev/null 2>&1; then
        echo "Failed to start local PHP server on ${BASE_URL}"
        exit 1
    fi
}

check_status() {
    local path="$1"
    local expected="$2"
    local code
    code="$(curl -s -o /dev/null -w "%{http_code}" "${BASE_URL}${path}")"
    if [[ "${code}" == "${expected}" ]]; then
        echo "[OK] ${path} -> ${code}"
    else
        echo "[FAIL] ${path} -> ${code} (expected ${expected})"
        FAILURES=$((FAILURES + 1))
    fi
}

check_redirect() {
    local path="$1"
    local expected_code="$2"
    local expected_location="$3"
    local headers
    local code
    local location

    headers="$(curl -s -D - -o /dev/null "${BASE_URL}${path}")"
    code="$(printf '%s' "${headers}" | awk '/^HTTP\// {print $2}' | tail -n 1)"
    location="$(printf '%s' "${headers}" | awk 'BEGIN{IGNORECASE=1} /^Location:/ {sub(/\r$/, "", $2); print $2}' | tail -n 1)"

    if [[ "${code}" == "${expected_code}" && "${location}" == "${expected_location}" ]]; then
        echo "[OK] ${path} -> ${code} ${location}"
    else
        echo "[FAIL] ${path} -> ${code} ${location} (expected ${expected_code} ${expected_location})"
        FAILURES=$((FAILURES + 1))
    fi
}

main() {
    cd "${ROOT_DIR}"
    start_server

    echo "Running MVC smoke test at ${BASE_URL}"
    check_status "/" "200"
    check_status "/about" "200"
    check_status "/artists" "200"
    check_status "/exhibitions" "200"
    check_status "/art-fairs" "200"
    check_status "/contact" "200"
    check_status "/exhibitions/8-hearts" "200"
    check_status "/this-route-does-not-exist" "404"

    check_redirect "/about.html" "301" "/about"
    check_redirect "/artists/artist-nguyen-thanh.html" "301" "/artists/nguyen-thanh"
    check_redirect "/artists/nguyen-thanh/2020.html" "301" "/artists/nguyen-thanh/2020"

    if [[ "${FAILURES}" -gt 0 ]]; then
        echo "Smoke test failed with ${FAILURES} issue(s)."
        exit 1
    fi

    echo "Smoke test passed."
}

main "$@"

