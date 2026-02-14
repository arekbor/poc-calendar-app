export interface User {
    id: number,
    username: string,
    email: string,
    calendar: {
        canDelete: boolean
    }
}