export class UserModel {
  public id: number|null;
  public email: string;
  public roles: string[];
  public active: boolean;
  public created: string;
  public updated: string;
  public sessions: any;

  constructor() {
    this.id = null;
    this.roles = [];
  }

  public hasRole(role: string): boolean {
    return this.roles.includes(role);
  }
}
