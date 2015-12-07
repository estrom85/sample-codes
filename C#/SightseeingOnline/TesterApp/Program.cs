using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using DataProvider.Providers;
using DataProvider.EntityModels.BackendModel;
using DataProvider.Interfaces;
using Logging;
using System.Diagnostics;

namespace TesterApp
{
    class Program
    {
        

        static void Main(string[] args)
        {
            
            //BackendDataProvider backend = new BackendDataProvider();
            /*
            PRODUCTS prod = new PRODUCTS();
            prod.NAME = "Test";
            prod.SYSTEM_UUID = Guid.NewGuid().ToString();
            PRODUCTCATEGORIES cat = new PRODUCTCATEGORIES();
            cat.NAME = "Test category";
            cat.SYSTEM_UUID = Guid.NewGuid().ToString();
            prod.PRODUCTCATEGORIES.Add(cat);

           // backend.addCategory(cat);
            backend.addProduct(prod);
            */
            /*
            ICollection<IProduct> prod = backend.GetTours(DateTime.MinValue, DataProvider.Enumerations.Role.NEW);
            foreach (IProduct p in prod)
            {
                Console.WriteLine(p.NAME);
            }
            */
            //IProduct prod = backend.GetTour(5);
            //Console.WriteLine(prod.NAME);
            int i = 0;
            SyncApp.SyncApplication app = new SyncApp.SyncApplication(new ConsoleLogger());
            //app.SubscribeLogger(new ConsoleLogger());
            Debug.WriteLine("Program start");
            //app.StartService();
            
            app._synchronize();
          
            //testFiltering();
            Console.WriteLine("Press any key");
            Console.ReadKey();
           // app.StopService();
        }

        public static void testFiltering()
        {
            int[] src = { 1, 2, 3, 6, 7, 10, 12 };
            int[] dst = { 2, 4, 5, 8, 9 };
            List<int> dst_list = dst.ToList();

            var i = dst_list.GetEnumerator();

            List<int> addTmp = new List<int>();
            List<int> remTmp = new List<int>();

            bool isEqual = false;
            foreach (var prod in src)
            {
                isEqual = false;
                while (i.MoveNext() && i.Current <= prod)
                {
                    if (i.Current < prod)
                    {
                        remTmp.Add(i.Current);
                        continue;
                    }
                    isEqual = true;
                    break;
                }

                if (isEqual)
                {
                    continue;
                }

                addTmp.Add(prod);
            }

            dst_list.RemoveAll(remTmp.Contains);
            dst_list.AddRange(addTmp);
            Console.WriteLine("Source");
            Println(src);
            Console.WriteLine("Dest");
            Println(dst);
            Console.WriteLine("Result");
            Println(dst_list);
        }

        public static void Println<T>(IEnumerable<T> list)
        {
            foreach(T item in list)
            {
                Console.Write(item + ", ");
            }
            Console.WriteLine();
        }
    }
}
