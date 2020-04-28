// @ts-ignore
export class BackendService {

  public static url: string = '/';

  public request = async (url: string, init?: RequestInit): Promise<Response> => {
    const response = await fetch(BackendService.url + url, init);

    if (response.status !== 200) {
      const data = await response.json();

      throw data.response;
    }

    return await response.json();
  };

}
