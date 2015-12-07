/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set.card_games32;

/**
 *
 * @author martinm
 */
public class CCard implements Comparable{
    private int mNumber;
    private int mType;
   
    
    private final String[] numbers=
    {
        "7","8","9","10","J","Q","K","A"
    };
    
    private final String[] types=
    {
      "Cerven","Zelen","Gula","Zalud" 
    };
    
    public CCard()
    {
        mNumber=-1;
        mType=-1;
    }
    
    public CCard(int number, int type)
    {
        if(number>8||number<0||type<0||type>4)
        {
            mNumber=-1;
            mType=-1;
        }
        else{
            
            mNumber=number;
            mType=type;
        }
    }
    
    public int getNumber()
    {
        return mNumber;
    }
    
    public String getNumberString()
    {
        if(mNumber<0) return "";
        return numbers[mNumber];
    }
    
    public int getType()
    {
        return mType;
    }
    
    public String getTypeString()
    {
        if(mType<0) return "";
        return types[mType];
    }

    @Override
    public int compareTo(Object o) {
       CCard card=(CCard) o;
       
        int compare;
        if(mType>card.mType)
            compare=1;
        else if(mType<card.mType)
            compare=-1;
        else
        {
            if(mNumber>card.mNumber)
                compare=1;
            else if(mNumber<card.mNumber)
                compare=-1;
            else compare=0;
        }
        
        return compare;
    }
}
