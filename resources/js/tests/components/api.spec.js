import useFetch from "../../composables/useFetch";
import { API } from "../../stores/api";
describe("test.vue", () => {
    beforeEach(() => {});

    test("test", () => {
        expect(1).toBe(1);
    });

    test("login", async () => {
        const response = await useFetch({
            url: API.login.path(),
            method: API.login.method,
            data: {
                username: "user1",
                password: "password1",
            },
        });
        console.log(response);
        expect(response.success).toBe(true);
    });
});
