/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package games_set.card_games32;

/**
 *
 * @author martinm
 */
public final class CPacket{
    private CCard[] mPacket=new CCard[32];
    private int size;
    /*
     * Constructor
     */
    /**
     * Constructor of Packet.
     * If fill is true, fills up packet with 32 cards. Every card is
     * different.
     * In other case creates empty packet
     * 
     * @param fill
     */
    public CPacket(boolean fill){
        if (fill)
        {
            int index=0;
            for (int type=0;type<4;type++)
            {
                for (int num=0;num<8;num++)
                {
                    mPacket[index]=new CCard(num,type);
                    index++;
                }
            }
            size=32;
            shuffle();
            
        }
        else
        {
            size=0;
        }
        
        
    }
    
    /*
     * Action methods
     */
    
    /**
     * Shuffles cards in packet
     */
    public void shuffle(){
        int index1;
        int index2;
        
        if (isEmpty())
            return;
        
        CCard temp;
        //sets number of shuffle cycles
        int num=(int)(Math.random()*100);
        
        //swaps two random positions
        for(int i=0;i<size*num;i++)
        {
            index1=(int)(Math.random()*size);
            index2=(int)(Math.random()*size);
            
            temp=mPacket[index1];
            mPacket[index1]=mPacket[index2];
            mPacket[index2]=temp;
            
        }
    }
    
    /**
     * Takes top card from Packet.
     * Number of cards diminishes after taking card.
     * If program attempts to take card from empty Packet
     * returns null
     * @return top card from Packet, if empty return null
     */
    public CCard takeCard(){
       CCard card;
       if(size>0)
       {
           card=mPacket[size-1];
           mPacket[size-1]=null;
           size--;
       }
       else
       {
           return null;
       }
       return card;
   }
   
    /**
     * Puts card on top of Packet
     * 
     * @param card card you want to put into deck
     * @return 
     * Top card of deck
     * @throws InvalidCardException
     */
    public CCard putCard(CCard card) throws InvalidCardException{
       if(size>31)
           throw new InvalidCardException(InvalidCardException.FULL_PACKET);
       
       if(card==null)
           throw new InvalidCardException(InvalidCardException.NULL_CARD);
       
       if(card.getNumber()<0||card.getType()<0)
           throw new InvalidCardException(InvalidCardException.INVALID_CARD);
       
       
       mPacket[size]=card;
       size++;
       return mPacket[size-1];
    }
    
    /*
     * Return methods
     */
    
    
    /**
     * Prits out packet
     */
    public void print(){
        for (int i=0;i<size;i++)
        {
            System.out.println(mPacket[i].getTypeString()+" -> "+mPacket[i].getNumberString());
        }
    }
    
    /**
     * Returns true if deck is empty
     * 
     * @return true if deck is empty
     */
    public boolean isEmpty(){
        return size==0;
    }
    
    /**
     * Returns number of cards in packet
     * @return size of packet
     */
    public int size(){
        return size;
    }
    
    /**
     * Returns top card from Packet
     * @return top card
     */
    public CCard getTopCard(){
        if (size>0)
            return mPacket[size-1];
        
        return null;
    }
}
