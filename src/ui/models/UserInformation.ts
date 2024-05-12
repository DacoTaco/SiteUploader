import { Base64 }from 'js-base64'

export class LoginResponse {
    constructor()
    {
        this.username = "";
        this.token = "";
    }

    username: string;
    token: string;
}

export enum UserType {
    Unknown,
    Admin,
    User,
    Guest
}

export class JwtModel {
    iss: string | null = null;
    iat: number | null = null; 
    nbf: number | null = null; 
    exp: number | null = null; 
    sub: string = "";
    role: string = UserType[UserType.Unknown];
    hash: string | null = null;
}

export class UserInformation{
    username: string;
    token: string;
    expirationDate: Date;
    userRole: UserType;

    constructor(token: string)
    {
        this.token = token;
        var payload = token.split('.')[1];
        var decoded = Base64.decode(payload);
        var jwt: JwtModel = JSON.parse(decoded);
        //Date wants it in miliseconds, not in seconds as the standard sends it :)
        this.expirationDate = new Date(jwt.exp as number * 1000);
        this.username = jwt.sub;    
        this.userRole = UserType[jwt.role as keyof typeof UserType];
    }
}