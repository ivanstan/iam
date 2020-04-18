// @ts-ignore
export class BackendService {

  public static url: string = '/';

  public request = async (url: string, init?: RequestInit): Promise<Response> => {
    const response = await fetch(BackendService.url + url, init);

    return await response.json();
  };

}
