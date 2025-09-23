// types/inertia.d.ts
export interface PageProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
    } | null;
  };
  flash: {
    success?: string;
  };
}
