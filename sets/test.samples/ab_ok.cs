using System;
class SumAB 
{  
  static void Main() 
  {
    string line = Console.ReadLine();
    do
    {
      var parts = line.Split();
      var A = int.Parse(parts[0]);
      var B = int.Parse(parts[1]);
      Console.WriteLine(A+B);    
      line = Console.ReadLine();
    }
    while (!string.IsNullOrEmpty(line));
  }
}
